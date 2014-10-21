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
- `(string)` **email** *A valid user email*
- `(string)` **pseudo** *alpha_dash pseudo between 2 and 55 chars*
- `(string)` **password** *password between 4 and 55 chars*
- `(string)` **password_confirmation** *password confirmation*

##### Login
| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| POST      | /api/users/login                  | Log a user to the app with is pseudo or email  | No     | OK     |

Post Parameters:
- `(string)` **pseudoOrEmail** *the user pseudo or email*
- `(string)` **password** *the user valid password*

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
- `(array)` **users_id** *list of users id to create the chat room*

| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| POST      | /api/rooms/name                   | Add a name to the room                         | Yes    | TODO   |

Post Parameters:
- `(int)` **romm_id** *the id of the room to name*
- `(string)` **name** *name to give to the room*

| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| GET       | /api/rooms/add/:user_id/:room_id  | Add a user to the room                         | Yes    | TODO   |
| GET       | /api/rooms/quit/:room_id          | Remove the user from the room                  | Yes    | TODO   |

### Message (Ratchet PHP websocket)
TODO
