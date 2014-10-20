#CrypTalker

CrypTalker is a open source Android and IOS application for strong (AES-256) crypted messaging. This repository contain only the application backend.

Not a single message is stored in database.

# API usage

### Users
| HTTP verb | Route               | Explanation                                   | Status |
| --------- |:--------------------|:----------------------------------------------|:------:|
| POST      | /api/users/register | Register a user to the app                    | OK     |
| POST      | /api/users/login    | Log a user to the app with is pseudo or email | OK     |
| GET       | /api/users/info/:id | Get all the info for a specified user         | TODO   | 

### Friends
| HTTP verb | Route                         | Explanation                                    | Status |
| --------- |:------------------------------|:-----------------------------------------------|:------:|
| GET       | /api/friends/add/:iduser      | Add a friend                                   | TODO   |
| GET       | /api/friends/validate/:iduser | Validate a friend invite                       | TODO   |
| GET       | /api/friends/block/:iduser    | Block a friend                                 | TODO   |

### Rooms
| HTTP verb | Route                             | Explanation                                    | Status |
| --------- |:----------------------------------|:-----------------------------------------------|:------:|
| POST      | /api/rooms/create                 | Create a room                                  | TODO   |
| POST      | /api/rooms/name/:iduser           | Add a name to the room                         | TODO   |
| GET       | /api/rooms/add/:iduser/:idroom    | Add a user to the room                         | TODO   |
| GET       | /api/rooms/quit/:idroom           | Remove the user from the room                  | TODO   |

### Message (websocket)
TODO
