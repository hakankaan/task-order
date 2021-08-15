# Task Order API

Start the server with **php artisan serve** command

## api/create-task

Requirements of "country" and "amount" fields are depends on "type" value.
Accepted types: "invoice_ops", "custom_ops", "common_ops"
Accepted currencies: €, ₺, $, £

**Request Body**:

> {
> "title": "Başlık 1",
> "type": "custom_ops",
> "country": "EN"
> }

or

> {
> "title": "Başlık 2",
> "type": "invoice_ops",
> "amount": {"currency": "€", "quantity": 1000}
> }

## api/add-prerequisities


**Request Body**:

> {
> "task_id": "task_8",
> "prerequisities": ["task_6", "task_1"]
> }

## api/tasks

Returns all tasks without order

## api/task-order

Returns the tasks with order

**Response**

    {
      "tasks": [
        {
          "id": "task_0",
          "title": "Task 0",
          "type": "custom_ops",
          "country": "EN",
          "prerequisities": [],
          "next": []
        },
        {
          "id": "task_1",
          "title": "Task 1",
          "type": "custom_ops",
          "country": "EN",
          "prerequisities": [],
          "next": [
            {
              "id": "task_5",
              "title": "Task 5",
              "type": "invoice_ops",
              "amount": {
                "currency": "£",
                "quantity": 499
              },
              "prerequisities": [
                "task_3",
                "task_1"
              ],
              "next": [
                {
                  "id": "task_6",
                  "title": "Task 6",
                  "type": "invoice_ops",
                  "amount": {
                    "currency": "₺",
                    "quantity": 5555
                  },
                  "prerequisities": [
                    "task_5"
                  ],
                  "next": []
                }
              ]
            }
          ]
        },
        {
          "id": "task_2",
          "title": "Task 2",
          "type": "common_ops",
          "prerequisities": [],
          "next": []
        },
        {
          "id": "task_3",
          "title": "Task 3",
          "type": "common_ops",
          "prerequisities": [],
          "next": [
            {
              "id": "task_4",
              "title": "Task 4",
              "type": "invoice_ops",
              "amount": {
                "currency": "€",
                "quantity": 1000
              },
              "prerequisities": [
                "task_3"
              ],
              "next": []
            },
            {
              "id": "task_5",
              "title": "Task 5",
              "type": "invoice_ops",
              "amount": {
                "currency": "£",
                "quantity": 499
              },
              "prerequisities": [
                "task_3",
                "task_1"
              ],
              "next": [
                {
                  "id": "task_6",
                  "title": "Task 6",
                  "type": "invoice_ops",
                  "amount": {
                    "currency": "₺",
                    "quantity": 5555
                  },
                  "prerequisities": [
                    "task_5"
                  ],
                  "next": []
                }
              ]
            }
          ]
        }
      ]
    }
