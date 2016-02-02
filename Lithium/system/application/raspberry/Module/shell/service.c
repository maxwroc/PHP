#include <stdlib.h>
#include <sys/types.h>
#include <unistd.h>
#include <string.h>
#include <stdio.h>

int main(int argc, char *argv[])
{
	if(argc != 3)
	{
		printf("Invalid number of args!");
		return 1;
	}

	setuid(0);

	/* WARNING: Only use an absolute path to the script to execute,
	*          a malicious user might fool the binary and execute
	*          arbitary commands if not.
	* */
	
	char command [50];
	
	sprintf(command, "/bin/sh /home/www/system/application/raspberry/Module/shell/service.sh %s %s", argv[1], argv[2]);

	system(command);

	return 0;
}