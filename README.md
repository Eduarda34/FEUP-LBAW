# NewsNet - lbaw2484

> NewsNet is a web-based collaborative news management platform, developed by a group of students in the context of the LBAW course. It is designed for news lovers and aspiring journalists to have a place to create, share and engage in news.

## Team

* Alexandre Henrique Silva dos Santos, up202108671@up.pt
* Maria Carlota Gomes Ribeiro Matos Leite, up202005428@up.pt
* Maria Eduarda Pacheco Mendes Araújo, up202004473@up.pt
* João Pedro Cardoso do Couto, up202006526@up.pt

## Prototype setup
```sh
# Pull latest image
docker pull gitlab.up.pt:5050/lbaw/lbaw2425/lbaw2484

# Remove container if already exists
docker rm lbaw2484

# Run container
docker run -d --name lbaw2484 -p 8001:80 gitlab.up.pt:5050/lbaw/lbaw2425/lbaw2484
```


## User credentials
* email: **cristiano_cr7_ronaldo@goat.pt**
* password: **12345678**