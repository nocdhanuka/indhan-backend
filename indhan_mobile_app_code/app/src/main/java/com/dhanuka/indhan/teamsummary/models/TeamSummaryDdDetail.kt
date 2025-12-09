package com.dhanuka.indhan.teamsummary.models

import java.io.Serializable

data class TeamSummaryDdDetail(
    val ECN: String,
    val app_permission_close_ticket: String,
    val app_permission_edit_ticket: String,
    val dd_address: String,
    val dd_alt_mobile_no: String,
    val dd_code: String,
    val dd_email: String,
    val dd_hq: String,
    val dd_id: String,
    val dd_image: String,
    val dd_mobile_no: String,
    val department: String,
    val designation: String,
    val name: String,
    var isChecked: Boolean
) : Serializable