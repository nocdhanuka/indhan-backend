package com.dhanuka.indhan.DemoRequestEvaluated.models

import java.io.Serializable

data class DemoRequestEvaluated(
    val Average: Int,
    val Awaited: Int,
    val Excellent: Int,
    val Overdue: Int,
    val Pending: Int,
    val Poor: Int,
    val demolist: List<DemoRequestEvaluatedlist>
) : Serializable