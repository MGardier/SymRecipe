# SymRecipe

SymRecipe est une application web qui permet aux utilisateurs de s'inscrire pour créer des recettes à partir d'ingrédients, ils peuvent ensuite décider de les partager avec la communauté ou de les garder en privé.

## Installation

EasyAdmin 4 requires PHP 8.0.2 or higher and Symfony 5.4 or higher. Run the
following command to install it in your application:

SymRecipe nécessite PHP 8.1 ou une version supérieur et Symfony 6.3 ou une version supérieur.
Pour faire fonctionner l'application vous allez devoir utiliser les commandes suivantes:

```
$ composer install
$ yarn install
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migrations:migrate
$ php bin/console doctrine:fixtures:load

```

Vous pouvez dès à présent vous connecter sur un compte en utilisant l'adresse e-mail de celui ci et le mot de passe "password" pour vous connecter et découvrir l'application.
