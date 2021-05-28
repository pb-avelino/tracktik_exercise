Feature: Tracktik

    Tracktik Developer Evaluation

    Background: Create Purchase order
        Given a purchase order with:
            | type       | price   | wired | remote |
            | console    | 650.57  | 2     | 2      |
            | television | 1425.36 | 0     | 2      |
            | television | 825.45  | 0     | 1      |
            | microwave  | 58.30   | 0     | 0      |

    Scenario: Check purchase order
        Then Purchase order has
            | type       | qty |
            | console    | 1   |
            | television | 2   |
            | microwave  | 1   |
        And total price of "all" items is grater than cero


    Scenario: Check consoles on purchase order
        Then Purchase order has
            | type    | qty |
            | console | 1   |
        And total price of "console" items is grater than cero

