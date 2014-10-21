#CrypTalker

CrypTalker is a open source Android and IOS application for strong (AES-256) crypted messaging. This repository contain only the application backend.

Not a single message is stored in database.

# API usage

Post request data are explained in the post parameters section of this readme.

### Users

##### Register
| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| POST      | /api/users/register               | Register a user to the app                     | No     | OK     |

Post Parameters:
- `(string)` email 
- `(string)` pseudo
- `(string)` password
- `(string)` password

##### Login
| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| POST      | /api/users/login                  | Log a user to the app with is pseudo or email  | No     | OK     |

Post Parameters:
- `(string)` pseudoOrEmail 
- `(string)` password

##### Info
| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| GET       | /api/users/info/:user_id          | Get all the info for a specified user          | Yes    | TODO   |

### Friends
| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| GET       | /api/friends/request/:user_id     | Make a friend request                          | Yes    | OK     |
| GET       | /api/friends/accept/:user_id      | Validate a friend invite                       | Yes    | OK     |
| GET       | /api/friends/block/:user_id       | Block a friend                                 | Yes    | OK     |
| GET       | /api/friends/unblock/:user_id     | Unblock a friend                               | Yes    | OK     |

### Rooms
| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| POST      | /api/rooms/create                 | Create a room                                  | Yes    | TODO   |

Post Parameters:
- `(array)` users_id (a list of users id to create the chat room)

| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| POST      | /api/rooms/name                   | Add a name to the room                         | Yes    | TODO   |

Post Parameters:
- `(array)` room_id (a list of users id to create the chat room)

| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| GET       | /api/rooms/add/:user_id/:room_id  | Add a user to the room                         | Yes    | TODO   |
| GET       | /api/rooms/quit/:room_id          | Remove the user from the room                  | Yes    | TODO   |

### Message (Ratchet PHP websocket)
TODO
