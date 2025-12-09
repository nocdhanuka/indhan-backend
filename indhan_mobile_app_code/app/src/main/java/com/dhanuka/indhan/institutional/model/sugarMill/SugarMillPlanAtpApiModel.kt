package com.dhanuka.indhan.institutional.model.sugarMill

data class SugarMillPlanAtpApiModel(
    val apiToken: String,
    val atp_data: List<SugarMillAtpData>
)

data class SugarMillAtpData(
    val date_time: String,
    val sugarmill_category: String,
    val fl_ecn: String,
    val fl_name: String,
    val fl_type: String,
    val is_manager: String,
    val sugarmill_id: String,
    val ticket_number: String
)