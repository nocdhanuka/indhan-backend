package com.dhanuka.indhan.DemoRequestEvaluated.models

import java.io.Serializable

data class DemoRequestEvaluatedResponse(
    val `data`: DemoRequestEvaluated,
    val message: String,
    val status: String,
    val status_code: Int
) : Serializable