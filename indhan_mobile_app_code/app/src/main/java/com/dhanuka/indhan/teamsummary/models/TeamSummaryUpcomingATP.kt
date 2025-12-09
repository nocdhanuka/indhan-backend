package com.dhanuka.indhan.teamsummary.models

import java.io.Serializable

data class TeamSummaryUpcomingATP(
    val api_token: String,
    val crop: String,
    val crop_id: String,
    val dd_id: String,
    val image: String,
    val name: String,
    val plan_atp_id: String,
    val plan_date: String,
    val product: String,
    val product_id: String
) : Serializable

