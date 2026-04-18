<?php

return [
    'free_student_limit' => (int) env('BILLING_FREE_STUDENT_LIMIT', 50),
    'monthly_per_billable_student' => (int) env('BILLING_MONTHLY_PER_BILLABLE_STUDENT', 100),
    'activation_per_new_student' => (int) env('BILLING_ACTIVATION_PER_NEW_STUDENT', 500),
];
