package com.dhanuka.indhan.institutional.model.influencers

data class InfluencersPlanAtpApiModel(
    val apiToken: String,
    val atp_data: List<InfluencersAtpData>
)

data class InfluencersAtpData(
    val date_time: String,
    val influencer_category: String,
    val fl_ecn: String,
    val fl_name: String,
    val fl_type: String,
    val is_manager: String,
    val influencer_id: String,
    val ticket_number: String
)