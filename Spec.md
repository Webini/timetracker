Roles
=====
## Admin
- Peut créer des projets
- Peut voir tous les projets
- Dispose des droits Project et User

## Project
- Peut voir les projets qu'il a créé
- Peut créer des projets
- Peut créer des issues pour les projets qu'il a créé
- Dispose des droits User

## User
- Ne peut pas créer de projets
- Peut voir les projets dans lequel il est assigné
- Peut créer des issues pour les projets dans lequel il est assigné et où il dispose des droits de creation d'issues


Project
=======
C'est un regroupement de tâche lié ou non a un TaskProvider

TaskProvider
============
Contient la configuration d'un task provider, peut être lié a plusieurs projets

Task
====
Une tâche est liée au projet, elle peut être archivée