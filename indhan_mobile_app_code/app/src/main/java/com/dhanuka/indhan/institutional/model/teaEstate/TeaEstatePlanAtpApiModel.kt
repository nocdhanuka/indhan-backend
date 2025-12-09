package com.dhanuka.indhan.institutional.model.teaEstate

data class TeaEstatePlanAtpApiModel(
    val apiToken: String,
    val atp_data: List<AtpData>
)

data class AtpData(
    val date_time: String,
    val estate_category: String,
    val fl_ecn: String,
    val fl_name: String,
    val fl_type: String,
    val is_manager: String,
    val tea_estate_id: String,
    val ticket_number: String
)