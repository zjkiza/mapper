#!/usr/bin/env python3

"""This is the docker manager module.

This module run Continuous Integration.
"""

__version__ = "0.1"
__author__ = "ZJKiza"

import time
import subprocess
import click
import sys
from rich.console import Console
from rich.markdown import Markdown
from pathlib import Path


@click.command()
@click.option(
    "--verbose/--no-verbose",
    default=False,
    help="Default is not verbose. Do you want verbose output of each check.",
    type=bool,
)
def run(verbose: bool) -> None:

    sys.path.append(str(Path(__file__).resolve().parent.parent))
    from bin.manager.docker_manager import DockerManager
    from bin.config.config import waiting_db_connection, commands, container_work_dir, container_php

    start = time.time()

    docker_manager = DockerManager.create(verbose=verbose, waiting_db=waiting_db_connection)
    docker_manager.run_container()

    result_of_tests = 0

    for command_name, command in commands.items():
        Console().print(f"[blue]Running `{command_name}`...[/blue]", width=120)

        output_process = subprocess.run(
            f'docker exec -w {container_work_dir} -it {container_php} sh -c "{command}"',
            shell=True,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            text=True,
        )

        result_of_tests = docker_manager.process_test_result(
            output_process, command_name, result_of_tests
        )

    total_time = round(time.time() - start)

    Console().print(Markdown("***"), width=120)

    if 0 != result_of_tests:
        Console().print(
            f"[red]ERROR! Not all checks passed, execution time was {total_time} seconds.[/red]",
            width=80,
        )
        Console().print(Markdown("***"), width=120)
        exit(1)

    Console().print(
        Markdown(f"### SUCCESS! All checks pass, execution time was {total_time} seconds."),
        width=120,
        style="green",
    )
    Console().print(Markdown("***"), width=120)

    docker_manager.down_container()

    Console().print(Markdown("***"), width=120)
    Console().print(Markdown("### End continuous integration."), width=120, style="green")
    Console().print(Markdown("***"), width=120)
    exit(0)


if __name__ == "__main__":
    Console().print(Markdown("***"), width=120)
    Console().print(Markdown("### Running continuous integration."), width=120, style="green")
    Console().print(Markdown("***"), width=120)
    run()
