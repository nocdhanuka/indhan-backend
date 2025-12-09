package com.dhanuka.indhan.teamsummary.models

import java.io.Serializable

data class UpcomingResponse(
    val `data`: UpcomingData,
    val message: String,
    val status: String,
    val status_code: Int
) : Serializable