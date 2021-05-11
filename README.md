# website
Site web du projet :)
Nomalement on a une synchro automatique de la branche main sur le serveur.

Adresse IP du serveur : 15.236.237.200

# Connection à l'API

Il est maintenant possible de se connecter à l'API pour envoyer des nouveaux refugees ou pour obtenir la dernière version des fields (et des listes).
Il est OBLIGATOIRE de transmettre un Application-Id dans les headers sinon la requête sera refusée

## Get fields :

Voici un exemple de requête et la réponse associée :

```
GET /api/fields HTTP/1.1
Host: 15.236.237.200:80
Authorization: Bearer YOUR_API_TOKEN
Accept: application/json
Content-Type: application/json
Application-id: YOUR_APPLICATION_ID
```

Exemple de réponse du serveur :
```
{
    "fields": {
            "unique_id": {
                "placeholder": "AAA-000001",
                "database_type": "string",
                "android_type": "EditText",
                "linked_list": "",
                "required": 0,
                "displayed_value": {
                    "eng": "Unique ID",
                    "fra": "ID Unique",
                    "esp": "Unico ID"
                }
            },
            "gender": {
                "placeholder": "F",
                "database_type": "string",
                "android_type": "Spinner",
                "linked_list": "Gender",
                "required": 1,
                "displayed_value": {
                    "eng": "Sex",
                    "fra": "Sexe",
                    "esp": "Sexo"
                }
            },
            "full_name": {
                "placeholder": "John Doe",
                "database_type": "string",
                "android_type": "EditText",
                "linked_list": "",
                "required": 1,
                "displayed_value": {
                    "eng": "Full Name",
                    "fra": "Nom complet",
                    "esp": "Full Name"
                }
            },
            ...
    },
    relations": {
            "BR": {
                "color": "000000",
                "importance": 1,
                "displayed_value": {
                    "eng": "Biological relationship",
                    "fra": "Relation biologique",
                    "esp": "Biological relationship"
                }
            },
            "NBR": {
                "color": "000000",
                "importance": 1,
                "displayed_value": {
                    "eng": "Non-biological relationship",
                    "fra": "Relation non biologique",
                    "esp": "Non-biological relationship"
                }
            },
            "TW": {
                "color": "000000",
                "importance": 1,
                "displayed_value": {
                    "eng": "Travelled with",
                    "fra": "A voyagé avec",
                    "esp": "Travelled with"
                }
            },
            "SA": {
                "color": "000000",
                "importance": 1,
                "displayed_value": {
                    "eng": "Saw",
                    "fra": "A vu",
                    "esp": "Saw"
                }
            },
            "SE": {
                "color": "000000",
                "importance": 1,
                "displayed_value": {
                    "eng": "Service",
                    "fra": "Service",
                    "esp": "Service"
                }
            }
    }
 }
```
## Add refugees :

Pour la format du fichier csv pour l'ajout des refugees, elle doit être comme suit:

Exemple:

```
'full_name','date','unique_id','nationality','alias','other_names','mothers_names','fathers_names','role','age','birth_date','date_last_seen','birth_place','gender','passport_number','embarkation_date','detention_place','embarkation_place','destination','route'
```

```
Ellis McGlynn MD,1989-12-28,ZSR-737417,7f025c60-be0f-4837-9176-57e755595995,Dr. Lurline Schumm DDS,Dr. Travis Beier,Helen Jast PhD,Prof. Aaliyah Auer,1602a8a5-ce72-425e-b42f-2be7867523f2,89,2010-05-24,1979-08-19,Leviport,4a10ec3f-5eec-492f-847c-0feaceb26d63,WOC-521635-WMCHCXIF,1970-03-10,Roubaix,Italy,8a329237-f566-4c49-91a9-b58e0f98e112,d8c9335b-2baa-4f54-813d-f07494d4c304,
```
NB: pour le moment, il y a des paramètres qui sont du format UUID, on va essayer de les regler le plus tot possible, pour le moment ça reste un POC(Proof Of Concept) que tout marche bien en important d'un fichier csv

Il faut maintenant envoyer une requête post avec le json à l'intérieur :

Exemple :

```
POST /api/manage_refugees HTTP/1.1
Host: 15.236.237.200:80
Accept: application/json
Content-Type: application/json
Authorization: Bearer YOUR_API_TOKEN
Application-id: YOUR_APPLICATION_ID
Content-Length: 256

[
    {
    "unique_id" : "ABC-000001",
    "full_name" : "full name",
    "nationality" : "FRA",
    "date" : "2021-04-12",
    "age" : 68,
    "gender" : "F"
    },
    {
    "unique_id" : "ABC-000002",
    "full_name" : "full name",
    "nationality" : "USA",
    "date" : "2021-04-12",
    "age" : 92,
    "gender" : "F"
    }
]
```

Le serveur répondra une erreur si la donnée envoyée n'est pas valide, je détaillerai plus tard comment et ce que vous
pouvez en faire, sinon un message de succès est envoyé. En cas de succès on recoit un message de type 201

## API post Link

L'API post permet aussi d'envoyer la liste des relations. Pour l'utiliser il faut :

- Que les deux refugees (personnes) soient déjà présente dans la database. Autrement dit, il faut d'abord effectuer une
  requete POST add refugee
- Préciser le `unique_id de chaque personne et la date à laquelle le champ a été créé
- Préciser une relation appartenant à la liste des relations prédéfinies.

On peut aussi préciser un détail sur la relation

```
POST /api/links HTTP/1.1
Host: 15.236.237.200:80
Accept: application/json
Content-Type: application/json
Authorization: Bearer YOUR_API_TOKEN
Application-id: YOUR_APPLICATION_ID
Content-Length: 249

[
    {
    "from_unique_id" : "ABC-000008",
    "to_unique_id" : "ABC-000009",``
    "relation" : "TW",
    "date" : "2021-05-14 8:51:53",
    "detail" : "at the port"
    }
]
```

En cas de succès on recoit un message de type `201`

### Erreurs possibles :

Si les données fournies ne sont pas bonnes, le serveur renvoie une erreur de type `422` :

```
{
    "message": "The given data was invalid.",
    "errors": {
        "0.refugee1_full_name": [
            "The 0.refugee1_full_name field is required."
        ],
        "1.refugee1_full_name": [
            "The 1.refugee1_full_name field is required."
        ],
        "2.refugee1_full_name": [
            "The 2.refugee1_full_name field is required."
        ],
        "3.refugee1_full_name": [
            "The 3.refugee1_full_name field is required."
        ],
        "0.refugee1_unique_id": [
            "The 0.refugee1_unique_id field is required."
        ],
        "1.refugee1_unique_id": [
            "The 1.refugee1_unique_id field is required."
        ],
        "2.refugee1_unique_id": [
            "The 2.refugee1_unique_id field is required."
        ],
        "3.refugee1_unique_id": [
            "The 3.refugee1_unique_id field is required."
        ],
        "0.refugee2_full_name": [
            "The 0.refugee2_full_name field is required."
        ],
        "1.refugee2_full_name": [
            "The 1.refugee2_full_name field is required."
        ],
        "2.refugee2_full_name": [
            "The 2.refugee2_full_name field is required."
        ],
        "3.refugee2_full_name": [
            "The 3.refugee2_full_name field is required."
        ],
        "0.refugee2_unique_id": [
            "The 0.refugee2_unique_id field is required."
        ],
        "1.refugee2_unique_id": [
            "The 1.refugee2_unique_id field is required."
        ],
        "2.refugee2_unique_id": [
            "The 2.refugee2_unique_id field is required."
        ],
        "3.refugee2_unique_id": [
            "The 3.refugee2_unique_id field is required."
        ],
        "0.relation": [
            "The 0.relation field is required."
        ],
        "1.relation": [
            "The 1.relation field is required."
        ],
        "2.relation": [
            "The 2.relation field is required."
        ],
        "3.relation": [
            "The 3.relation field is required."
        ]
    }
}
```
On retrouve le détail de tous les champs mal renseignés

## Obtenir son API Token :

Il faut se créer un compte sur le site puis se rendre dans profil. Ici il y a une section permettant de consulter son api token.

## Docker ou comment bosser de chez moi :

À partir de `.env.example` créer le fichier `.env` avec les bons logs (à la racine du dossier) :
`cp .env.example .env`

Pour lancer le projet, on peut plus ou moins utiliser docker. Pour ce faire 

via docker : 

    docker-compose build && docker-compose up -d

Ou mieux : (bien avoir les droits d'éxecution sur start-laravel)

    ./start-laravel

On peut alors consulter `127.0.0.1:8080`

Pour stopper le tout :

    docker-compose down

### Associer la database

Pour pouvoir bosser avec la database, il est nécessaire de motifier le fichier `src/.env`.

Voici un exemple de ce qu'il doit contenir, où les valeurs `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` doivent correspondre aux informations saisies dans le `.env` de racine (utilisé par docker pour générer le service de mongodb).

```
DB_CONNECTION=mysql
DB_HOST=sql
DB_PORT=3306
DB_DATABASE=DB_DATABASE_NAME
DB_USERNAME=user_example
DB_PASSWORD=my_passwd
```
### Associer un serveur smtp

Pour pouvoir bosser avec la verification(envoie en général) des emails, il est nécessaire de motifier le fichier `src/.env`.

Voici un exemple de ce qu'il doit contenir, où les valeurs `MAIL_MAILER`, `MAIL_HOST`, `MAIL_USERNAME`, `MAIL_PASSWORD` doivent correspondre aux informations saisies dans le `.env` de racine (utilisé mailtrap.io pour créer un serveur smtp gratuit).

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=SMTP_USERNAME
MAIL_PASSWORD=SMTP_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=address_email
```


# Lancement de Laravel

Après avoir exécuté `./start_laravel`, il faut se connecter à ce conteneur et exécuter les commandes suivantes :

```
composer update
php artisan migrate --seed
```

Cela permet de télécharger les dépendances et de mettre à jour la base de donnée avec les fichiers nécessaires.



## Séance du 02/04/21

- Mise en place des premiers scripts de migration de la database
- Mise en place du Docker de manière stable
- Rédaction de premier scripts (toujours dans le domaine de la DB)



## Séance du 03/03/21

Brainstorm sur les choses à implémenter, discussion sur le back et le front rapidement, choix de Laravel et on monte laravel sur le serveur.

![Brainstorm](/img/website-brainstorm.png)

## Séance du 15/03/21

Travail sur Laravel, on a regardé comment on pouvait faire les migrations DB (ça tourne sur le serveur).
Réflexion sur le contenu des pages du site. [Ici](https://www.figma.com/file/SfFnr65viq4wDuNNmEdbqp/Untitled?node-id=0%3A1).
Réflexion sur les liens entre les pages.

