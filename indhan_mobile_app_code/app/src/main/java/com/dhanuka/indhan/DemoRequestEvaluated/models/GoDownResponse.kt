package com.dhanuka.indhan.DemoRequestEvaluated.models

import androidx.room.Entity
import androidx.room.PrimaryKey

data class GoDownResponse(
    val `data`: List<GoDownData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class GoDownData(
    val contact_number: String,
    val contact_person: String,
    val created_at: String,
    val district_id: Int,
    val godown: String,
    @PrimaryKey
    val godown_id: Int,
    val state_id: Int,
    val status: String,
    val updated_at: String
)