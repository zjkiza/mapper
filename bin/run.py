#!/usr/bin/env python3

"""This is the docker manager module.

This module run docker container.
"""

__version__ = "0.1"
__author__ = "ZJKiza"

import click
import sys
from pathlib import Path


@click.command()
@click.option("--verbose/--no-verbose", default=False, help="Default is not verbose.", type=bool)
@click.option(
    "--waiting_db_connection/--no-waiting_db_connection",
    default=False,
    help="Default is not waiting db connection.",
    type=bool,
)
def run(verbose: bool, waiting_db_connection: bool) -> None:
    sys.path.append(str(Path(__file__).resolve().parent.parent))
    from bin.manager.docker_manager import DockerManager

    docker_manager = DockerManager.create(verbose=verbose, waiting_db=waiting_db_connection)
    docker_manager.run_container()


if __name__ == "__main__":
    run()
