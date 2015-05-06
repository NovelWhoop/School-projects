SERVER          = server
CLIENT          = client
SERVER_SOURCES  = server.c
CLIENT_SOURCES  = client.c

CFLAGS         = -g -std=c99

CC              = gcc
SERVER_OBJECTS  = $(SERVER_SOURCES:.c=.o)
CLIENT_OBJECTS  = $(CLIENT_SOURCES:.c=.o)

.SUFFIXES: .c .o

.c.o:
		$(CC) $(CFLAGS) -c $<

all:		$(SERVER) $(CLIENT)

rebuild:	clean all

$(SERVER):	$(SERVER_OBJECTS)
		$(CC) $(SERVER_OBJECTS) $(LDFLAGS) -o $@

$(CLIENT):	$(CLIENT_OBJECTS)
		$(CC) $(CLIENT_OBJECTS) $(LDFLAGS) -o $@

clean:
	rm -fr core* *~ $(SERVER_OBJECTS) $(CLIENT_OBJECTS) $(SERVER) $(CLIENT) .make.state .sb
