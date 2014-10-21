#CrypTalker

CrypTalker is a open source Android and IOS application for strong (AES-256) crypted messaging. This repository contain only the application backend.

Not a single message is stored in database.

# API usage

### Users
| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| POST      | /api/users/register               | Register a user to the app                     | No     | OK     |
| POST      | /api/users/login                  | Log a user to the app with is pseudo or email  | No     | OK     |
| GET       | /api/users/info/:iduser           | Get all the info for a specified user          | Yes    | TODO   | 

### Friends
| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| GET       | /api/friends/request/:iduser      | Make a friend request                          | Yes    | Ok     |
| GET       | /api/friends/validate/:iduser     | Validate a friend invite                       | Yes    | TODO   |
| GET       | /api/friends/block/:iduser        | Block a friend                                 | Yes    | TODO   |

### Rooms
| HTTP verb | Route                             | Explanation                                    | Logged | Status |
|:----------|:----------------------------------|:-----------------------------------------------|:------:|:------:|
| POST      | /api/rooms/create                 | Create a room                                  | Yes    | TODO   |
| POST      | /api/rooms/name/:iduser           | Add a name to the room                         | Yes    | TODO   |
| GET       | /api/rooms/add/:iduser/:idroom    | Add a user to the room                         | Yes    | TODO   |
| GET       | /api/rooms/quit/:idroom           | Remove the user from the room                  | Yes    | TODO   |

### Message (Ratchet PHP websocket)
TODO
