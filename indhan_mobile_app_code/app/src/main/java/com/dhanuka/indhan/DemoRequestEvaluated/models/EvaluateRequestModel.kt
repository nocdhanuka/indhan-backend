package com.dhanuka.indhan.DemoRequestEvaluated.models

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity
data class EvaluateRequestModel(
    @PrimaryKey(autoGenerate = true) val id: Int,
    val description: String?,
    val status: String,
    val demo_request_id: String,
    val feedbackImage: String,
    val feedbackImageName: String
)