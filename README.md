[![Codacy Badge](https://app.codacy.com/project/badge/Grade/c87fa051d93148149bf80de3e5c9c21f)](https://www.codacy.com/gh/fabienVernieres/BileMo/dashboard?utm_source=github.com&utm_medium=referral&utm_content=fabienVernieres/BileMo&utm_campaign=Badge_Grade)

# Projet Symfony BileMo

---

Projet de formation : Créez un web service exposant une API

## Table of Contents

1. [Informations générales](#informations-generales)
2. [Technologies](#technologies)
3. [Installation](#installation)
4. [Prise en main](#prise-en-main)

## Informations générales

La démonstration du projet est disponible à cette adresse :
[bilemo.fabienvernieres.com](https://bilemo.fabienvernieres.com)

Auteur du projet : fabien Vernières
[fabienvernieres.com](https://fabienvernieres.com)

Date : janvier 2023

## Technologies

Projet réalisé avec le framework Symfony version 6.

Cette application est optimisée pour un serveur PHP 8.0.0

Une base données MYSQL est requise.

Le frontend est réalisé avec le framework Boostrap.

## Installation

Téléchargez le dossier ZIP du code ou cloner le dépôt sur votre périphérique.

```text
git clone https://github.com/fabienVernieres/BileMo.git
```

Installer `composer`

[getcomposer.org/download/](https://getcomposer.org/download/)

Puis exécutez la commande suivante:

```text
composer install
```

Créez la base de données de l'application:

```text
php bin/console doctrine:database:create
```

Modifiez le fichier `.env` à la racine du projet afin de permettre la connexion à votre base de données:

```text
DATABASE_URL="mysql://root:password@127.0.0.1:3306/dbname?serverVersion=8"
```

Effectuez une misé à jour de votre base de données:

```text
php bin/console doctrine:migrations:migrate
```

Pour créer un jeu de données:

```text
php bin/console doctrine:fixtures:load
```

Lancer le serveur Symfony:

```text
symfony server:start -d
```

Votre site est maintenant accessible à l'adresse suivante

[https://127.0.0.1:8000](https://127.0.0.1:8000)

## Prise en main

Une fois le projet installé, vous pouvez créer un compte pour accéder à la documentation de l'API et faire vos premières requêtes. Je vous recommande l'application Postman pour vos tests API.
