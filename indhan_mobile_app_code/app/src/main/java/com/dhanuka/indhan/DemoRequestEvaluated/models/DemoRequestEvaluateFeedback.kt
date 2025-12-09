package com.dhanuka.indhan.DemoRequestEvaluated.models

import java.io.Serializable

data class DemoRequestEvaluateFeedback(
    val creation_at: String,
    val status: String,
    val feedbackImage: String
) : Serializable