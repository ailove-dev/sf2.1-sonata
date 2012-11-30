Feature: Base
    Проверка базовой страницы

    Scenario: Главная
        Given I am on "/"
        #Then the response status code should be 200
        And I should see "Ailove world"
        #And show last response

