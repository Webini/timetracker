Roles
=====
## Super Admin
- Dispose des droits Admin, Project Manager et User
- Peut créer des users Super Admin, Admin, Project Manager et user

## Admin
- Peut créer des projets
- Peut voir tous les projets
- Peut créer des users admin, project manager, user
- Dispose des droits Project manager et User

## Project manager
- Peut voir les projets qu'il a créé
- Peut créer des projets
- Peut créer des issues pour les projets qu'il a créé
- Peut créer des users user
- Dispose des droits User

## User
- Ne peut pas créer de projets
- Peut voir les projets dans lequel il est assigné
- Peut créer des issues pour les projets dans lequel il est assigné et où il dispose des droits de creation d'issues
- Ne peut pas créer d'users

Project
=======
C'est un regroupement de tâche lié ou non a un TaskProvider

TaskProvider
============
Contient la configuration d'un task provider, peut être lié a plusieurs projets

Task
====
Une tâche est liée au projet, elle peut être archivée
