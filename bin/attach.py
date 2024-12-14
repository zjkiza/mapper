#!/usr/bin/env python3

import click
import os
from rich.console import Console
from utility.docker_manager_factory import create_docker_manager
from config import container_php, container_work_dir


@click.command()
@click.option('--command', default='"/bin/bash"', help='A command to execute when attaching to container.')
def attach(command: str) -> None:
    docker_manager = create_docker_manager(verbose=True)
    docker_manager.is_containers_running()

    Console().print(
        f'Leaving host environment and attaching to service {container_php}...',
        width=80,
        style="yellow"
    )

    os.system(f'docker exec -w {container_work_dir} -it {container_php} bash -c "{command}"')


if __name__ == '__main__':
    attach()
