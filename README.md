for instllation :
Cloner le dépôt : git clone https://github.com/manelyasmine/aladaviesTesting.git
Installer les dépendances : cd aladaviesTesting && composer install
Configurer la base de données : Modifier le fichier .env avec vos informations de connexion à la base de données.
Créer les migrations : php bin/console doctrine:migrations:migrate
to run application use symfony server:start 


Employee:
1. Create Leave Request

    URL: /employes/leave-requests

    Method: POST

    Request Body (JSON):
   {
    "employeeId": <employee_id>,
    "startDate": "YYYY-MM-DD",
    "endDate": "YYYY-MM-DD",
    "comment": "Leave request comment"
}
employeeId: The ID of the employee requesting the leave.
startDate: The start date of the leave request.
endDate: The end date of the leave request.
comment: An optional comment or reason for the leave request.
2. Get Leave Requests

    URL: /employes/{id}/leave-requests
    Method: GET
   Request Body(json):
    {
    "employeeId": <employee_id>
    }


   2/Manager:
    Update Leave Request

    URL: /manager/leave-requests/{demandeCongesId}

    Method: PUT

    Request Body (JSON):
   {
    "managerId": <manager_id>,
    "action": "approve" | "reject",
    "comment": "Optional comment for rejection"
}
managerId: The ID of the manager.
action: The action to perform (either "approve" or "reject").
comment: A required comment if the action is "reject".
2. Get Leave Requests

    URL: /manager/{id}/leave-requests
    Method: GET
   Body (Json):
   {"managerId":<id_manager>}
   
   

