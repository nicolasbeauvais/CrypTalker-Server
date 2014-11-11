#CrypTalker

CrypTalker is a open source Android and IOS application for strong (AES-256) crypted messaging. This repository contain only the application backend.

Not a single message is stored in database.

# API usage

Post request data are explained in the post parameters section of this readme.

### Users

| HTTP verb | Route                             | Explanation                                    | Logged |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|
| POST      | /api/users/register               | Register a user to the app                     | No     |
| POST      | /api/users/login                  | Log a user to the app with is pseudo or email  | No     |
| POST      | /api/users/login-with-token       | Log a user to the app with is id and token     | No     |
| GET       | /api/users/logout                 | Logout a user                                  | Yes    |
| GET       | /api/users/info/                  | Get all the info for the logged user           | Yes    |

Register Parameters:
- `(string)` **email** *A valid user email*
- `(string)` **pseudo** *Alpha_dash pseudo between 2 and 55 chars*
- `(string)` **password** *Password between 4 and 55 chars*
- `(string)` **password_confirmation** *Password confirmation*
- `(string)` **mobile_id** *Google CLoud Messaging user<=>app id*

Login Parameters:
- `(string)` **pseudoOrEmail** *The user pseudo or email*
- `(string)` **password** *The user valid password*
- `(string)` **mobile_id** *Google CLoud Messaging user<=>app id*

LoginWithToken Parameters:
- `(string)` **mobile_id** *Google CLoud Messaging user<=>app id*
- `(string)` **token** *The user remember token*

Register & Login return a user remember token to store in the client app (One different token by user<=>app).

##### Info

| HTTP verb | Route                             | Explanation                                    | Logged |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|
| POST      | /api/friends/request/             | Make a friend request                          | Yes    |
| GET       | /api/friends/accept/:user_id      | Validate a friend invite                       | Yes    |
| GET       | /api/friends/block/:user_id       | Block a friend                                 | Yes    |
| GET       | /api/friends/unblock/:user_id     | Unblock a friend                               | Yes    |

Request Parameters:
- `(string)` **pseudo** *Alpha_dash pseudo between 2 and 55 chars*

### Rooms

| HTTP verb | Route                             | Explanation                                    | Logged |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|
| POST      | /api/rooms/create                 | Create a room                                  | Yes    |
| POST      | /api/rooms/name                   | Add a name to the room                         | Yes    |
| GET       | /api/rooms/add/:user_id/:room_id  | Add a user to the room                         | Yes    |
| GET       | /api/rooms/quit/:room_id          | Remove the user from the room                  | Yes    |

Create Parameters:
- `(array)` **users_id** *List of users id to create the chat room*

Name Parameters:
- `(int)` **room_id** *The id of the room to name*
- `(string)` **name** *Name to give to the room*

### Message (Ratchet PHP websocket)
| HTTP verb | Route                             | Explanation                                    | Logged |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|
| POST      | /api/messages/new                 | Send a message                                 | Yes    |

New Parameters:
- `(int)` **room_id** *The id of the room to send the message*
- `(string)` **message** *name to give to the room*
