#!/usr/bin/env python3

import click
from utility.docker_manager_factory import create_docker_manager


@click.command()
@click.option('--verbose/--no-verbose', default=False, help='Default is not verbose.', type=bool)
@click.option('--waiting_db_connection/--no-waiting_db_connection', default=False,
              help='Default is not waiting db connection.', type=bool)
def run(verbose: bool, waiting_db_connection: bool) -> None:
    docker_manager = create_docker_manager(verbose=verbose, waiting_db=waiting_db_connection)
    docker_manager.run_container()


if __name__ == '__main__':
    run()

