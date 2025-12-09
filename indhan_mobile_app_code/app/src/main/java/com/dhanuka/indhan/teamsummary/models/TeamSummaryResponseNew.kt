package com.dhanuka.indhan.teamsummary.models

import java.io.Serializable

data class TeamSummaryResponseNew(
    val `data`: TeamSummaryData,
    val message: String,
    val status: String,
    val status_code: Int
) : Serializable