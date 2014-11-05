#CrypTalker

CrypTalker is a open source Android and IOS application for strong (AES-256) crypted messaging. This repository contain only the application backend.

Not a single message is stored in database.

# API usage

Post request data are explained in the post parameters section of this readme.

### Users

| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| POST      | /api/users/register               | Register a user to the app                     | No     | OK     |
| POST      | /api/users/login                  | Log a user to the app with is pseudo or email  | No     | OK     |
| GET       | /api/users/info/:user_id          | Get all the info for a specified user          | Yes    | TODO   |

Register Parameters:
- `(string)` **email** *A valid user email*
- `(string)` **pseudo** *alpha_dash pseudo between 2 and 55 chars*
- `(string)` **password** *password between 4 and 55 chars*
- `(string)` **password_confirmation** *password confirmation*
- `(string)` **mobile_id** *Google CLoud Messaging user<=>app id*

Login Parameters:
- `(string)` **pseudoOrEmail** *the user pseudo or email*
- `(string)` **password** *the user valid password*
- `(string)` **mobile_id** *Google CLoud Messaging user<=>app id*

Register & Login return the a user remember token to store in the client app (on different token by user<=>app).

##### Info

| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| GET       | /api/friends/request/:user_id     | Make a friend request                          | Yes    | OK     |
| GET       | /api/friends/accept/:user_id      | Validate a friend invite                       | Yes    | OK     |
| GET       | /api/friends/block/:user_id       | Block a friend                                 | Yes    | OK     |
| GET       | /api/friends/unblock/:user_id     | Unblock a friend                               | Yes    | OK     |

### Rooms

| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| POST      | /api/rooms/create                 | Create a room                                  | Yes    | OK     |
| POST      | /api/rooms/name                   | Add a name to the room                         | Yes    | OK     |
| GET       | /api/rooms/add/:user_id/:room_id  | Add a user to the room                         | Yes    | OK     |
| GET       | /api/rooms/quit/:room_id          | Remove the user from the room                  | Yes    | OK     |

Create Parameters:
- `(array)` **users_id** *list of users id to create the chat room*

Name Parameters:
- `(int)` **room_id** *the id of the room to name*
- `(string)` **name** *name to give to the room*

### Message (Ratchet PHP websocket)
| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| POST      | /api/messages/new                 | Send a message                                 | Yes    | OK     |
