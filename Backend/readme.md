# Requirements
To run this project you will need a computer with PHP and composer installed.

# Install
To install the project, you just have to run `composer install` to get all the dependencies

# Running the tests
After installing the dependencies you can run the tests with this command `vendor/bin/behat`.
The result should look like this :
![behat.png](behat.png)

# Step 3: Code Quality & CI/CD

### Code Quality Tools

Pour assurer la qualité, la maintenabilité et la robustesse du code, j'intégrerais les outils suivants, qui sont des standards dans l'écosystème PHP.

*   **1. PHP-CS-Fixer (PHP Coding Standards Fixer)**
    *   **Quoi :** Un outil qui analyse et corrige automatiquement le style du code pour qu'il respecte une norme (comme **PSR-12**).
    *   **Pourquoi :** Pour garantir une **cohérence de style** absolue dans tout le projet. Un code cohérent est plus facile et rapide à lire pour toute l'équipe, ce qui réduit les débats sur le style lors des revues de code.
    *   _**Statut :** Cet outil a été intégré au projet et le code a été formaté en conséquence._

*   **2. PHPStan (Analyseur Statique)**
    *   **Quoi :** Un analyseur qui trouve des bugs sans exécuter le code. Il vérifie la cohérence des types de données, détecte des erreurs logiques (appel de méthode sur une variable potentiellement `null`, etc.).
    *   **Pourquoi :** Pour **prévenir les bugs** avant même qu'ils n'atteignent la production. C'est un filet de sécurité qui augmente considérablement la fiabilité de l'application, en particulier lorsque le projet grandit.

*   **3. PHPUnit (Framework de Test)**
    *   **Quoi :** Le framework de référence pour les tests unitaires et d'intégration en PHP.
    *   **Pourquoi :** Pour **compléter les tests Behat**. Tandis que Behat teste le comportement de l'application "de l'extérieur" (BDD), PHPUnit permet de tester chaque classe individuellement ("de l'intérieur"). Cela garantit que chaque composant fonctionne comme prévu et aide à localiser les régressions très rapidement.

### Processus CI/CD (Intégration et Déploiement Continus)

Pour automatiser la validation et le déploiement, je mettrais en place un pipeline de CI/CD (par exemple avec GitHub Actions). Les actions nécessaires seraient :

1.  **Déclenchement :** Le processus démarre automatiquement à chaque `push` ou Pull Request sur la branche principale.

2.  **Préparation de l'Environnement :** Le serveur de CI récupère le code, installe la bonne version de PHP, les dépendances (`composer install`) et prépare les services nécessaires comme une base de données PostgreSQL.

3.  **CI - Validation de la Qualité :** Le pipeline exécute une série de vérifications. Un échec à l'une de ces étapes bloque le processus et notifie l'équipe.
    *   **Formatage (Lint) :** Lancer `php-cs-fixer` pour vérifier le respect des normes de style.
    *   **Analyse Statique :** Lancer `phpstan` pour détecter des bugs potentiels.
    *   **Tests :** Lancer la suite de tests `behat` pour s'assurer qu'aucune fonctionnalité n'est cassée.

4.  **CD - Déploiement :** Si toutes les vérifications réussissent, le code est automatiquement déployé :
    *   Sur un environnement de **staging (pré-production)** pour une dernière validation manuelle.
    *   Sur l'environnement de **production** après une action manuelle (comme la fusion de la Pull Request).