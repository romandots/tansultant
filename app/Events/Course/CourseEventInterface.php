<?php
/**
 * File: CourseEventInterface.php
 * Author: Roman Dots <romandots@brainex.co>
 * Date: 2020-2-20
 * Copyright (c) 2020
 */

namespace App\Events\Course;

interface CourseEventInterface
{
    public function getCourse(): \App\Models\Course;
    public function getUser(): \App\Models\User;
}