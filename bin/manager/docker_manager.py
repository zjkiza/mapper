from __future__ import annotations
from typing import final, List, Optional
from dotenv import dotenv_values
from rich.console import Console
from rich.markdown import Markdown
from bin.config.config import container_db, docker_compose_files_list, containers, phpunit_code_error_bypass, ping_db
import re
import os
import time
import subprocess
from pydantic import BaseModel, Field


@final
class DockerManager(BaseModel):

    verbose: bool
    docker_compose_files_list: List[str]
    containers: List[str]
    container_db: Optional[str] = None
    ping_db: Optional[str] = None
    waiting_db_connection: bool = False
    phpunit_code_error_bypass: bool = False
    console: Console = Field(default_factory=Console)
    working_directory: str = os.path.abspath(os.path.join(os.path.dirname(__file__), '..', '..'))

    class Config:
        arbitrary_types_allowed = True

    @property
    def docker_compose_files(self) -> str:
        return ' -f '.join(self.docker_compose_files_list)

    @classmethod
    def create(cls, verbose: bool = True, waiting_db: bool = False) -> DockerManager:
        return cls(
            verbose=verbose,
            waiting_db_connection=waiting_db,
            docker_compose_files_list=docker_compose_files_list,
            containers=containers,
            container_db=container_db,
            ping_db=ping_db,
            phpunit_code_error_bypass=phpunit_code_error_bypass
        )

    def run_container(self) -> None:
        os.chdir(self.working_directory)
        self.is_environment_appropriate()

        self.console.print(Markdown('# Running docker containers ...'), width=120, style="green")

        result = subprocess.run(
            f'docker-compose -f {self.docker_compose_files} up -d --build {"> /dev/null" if not self.verbose else ""}',
            shell=True,
            stdout=subprocess.PIPE
        )

        if result.returncode != 0:
            self.console.print(f'[bold red]ERROR container "{self.docker_compose_files}" failed to start![/bold red]')
            exit(1)

        self.is_containers_running()

        if self.container_db and self.waiting_db_connection:
            self.waiting_database_connection()

    def down_container(self) -> None:
        result = subprocess.run(
            f'docker-compose -f {self.docker_compose_files} down {"> /dev/null" if not self.verbose else ""}',
            shell=True,
            stdout=subprocess.PIPE
        )

        if result.returncode != 0:
            self.console.print(f'[bold red]ERROR container "{self.docker_compose_files}" failed to down![/bold red]')
            exit(1)

        self.console.print(Markdown('***'), width=120)
        self.console.print(Markdown('### Tearing down docker containers.'), width=120, style="green")
        self.console.print(Markdown('***'), width=120)

    def process_test_result(self, output_process: subprocess.CompletedProcess, command_name: str,
                            result_of_tests: int) -> int:
        if self.phpunit_code_error_bypass and command_name == 'composer run phpunit':
            if "FAILURES!" in output_process.stdout or "Failures:" in output_process.stdout:
                self.print_result('Failed.', 'red', output_process, command_name)
                return result_of_tests + 1
            else:
                self.print_result('Pass.', 'green', output_process, command_name)
                return result_of_tests

        if output_process.returncode:
            self.print_result('Failed.', 'red', output_process, command_name)
            return result_of_tests + 1
        else:
            self.print_result('Pass.', 'green', output_process, command_name)
            return result_of_tests

    def display_output(self, output_process: subprocess.CompletedProcess, name: str) -> None:
        self.console.print(f'[yellow]Displaying the contents of the started test "{name}":[/yellow]', width=120)

        clean_text = re.sub(r'\x1B(?:[@-Z\\-_]|\[[0-?]*[ -/]*[@-~])', '', output_process.stdout)
        self.console.print(clean_text)
        self.console.print(f'[yellow]End of display "{name}".[/yellow]', width=120)

    def print_result(self, message: str, style: str, output_process: subprocess.CompletedProcess, name: str) -> None:
        self.console.print(message, width=80, style=style)
        if self.verbose:
            self.display_output(output_process, name)

    def waiting_database_connection(self) -> None:
        self.console.print(f'[yellow]Waiting for the database "{self.container_db}" to be ready......[/yellow]')

        for i in range(3):
            try:
                subprocess.run(
                    f'docker exec {self.container_db} sh -c "{self.ping_db}"',
                    shell=True,
                    check=True
                )
                self.console.print('[yellow]The database is ready![/yellow]')
                break
            except subprocess.CalledProcessError:
                self.console.print(f"Attempt {i + 1}: The database is not ready yet...")
                time.sleep(30)
        else:
            self.console.print('[bold red]Database not ready after 90 seconds. Check the configuration![/bold red]')
            exit(1)

    def is_environment_appropriate(self) -> None:
        configuration = dotenv_values('.env')

        if configuration.get('APP_ENV') == 'prod':
            self.console.print(
                '[bold red]ERROR: This script can be executed only in developer and test environment![/bold red]')
            exit(1)

    def is_containers_running(self) -> None:
        for container_name in self.containers:
            if container_name and not self.__is_container_running(container_name):
                self.console.print(f'[bold red]Container "{container_name}" not mounted or not found.[/bold red]')
                exit(1)

    def __is_container_running(self, container_name: str) -> bool:
        try:
            result = subprocess.run(
                ['docker', 'ps'],
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                text=True
            )
            return container_name in result.stdout
        except Exception as e:
            self.console.print(f'[bold red]Error while checking container status: {e}[/bold red]')
            exit(1)
