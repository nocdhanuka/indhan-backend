package com.dhanuka.indhan.teamsummary.models

import java.io.Serializable

data class TeamSummaryResponse(
    val `data`: Data,
    val message: String,
    val status: String,
    val status_code: Int
) : Serializable {
    data class Data(
        val averageVisit: Double,
        val ddCount: Int,
        val ddDetail: List<DdDetail>,
        val farmerCount: Int,
        val upcomingATP: List<UpcomingATP>,
        val villageCount: Int
    ) : Serializable {
        data class DdDetail(
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

        data class UpcomingATP(
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
    }
}