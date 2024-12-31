#!/usr/bin/env python3

"""This is the docker manager module.

This module enters the running docker container.
"""

__version__ = "0.1"
__author__ = "ZJKiza"

import click
import os
import sys
from rich.console import Console
from pathlib import Path


@click.command()
@click.option(
    "--command", default='"/bin/bash"', help="A command to execute when attaching to container."
)
def attach(command: str) -> None:

    sys.path.append(str(Path(__file__).resolve().parent.parent))
    from bin.manager.docker_manager import DockerManager
    from bin.config.config import container_work_dir, container_php

    docker_manager = DockerManager.create(verbose=True)
    docker_manager.is_containers_running()

    Console().print(
        f"Leaving host environment and attaching to service {container_php}...",
        width=80,
        style="yellow",
    )

    os.system(f'docker exec -w {container_work_dir} -it {container_php} bash -c "{command}"')


if __name__ == "__main__":
    attach()
