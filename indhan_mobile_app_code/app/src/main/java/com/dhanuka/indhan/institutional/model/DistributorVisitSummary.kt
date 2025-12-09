package com.dhanuka.indhan.institutional.model

import androidx.room.Entity
import androidx.room.PrimaryKey

data class DistributorVisitSummary(
    val base_url: String,
    val crr_date: String,
    val `data`: List<DistributorVisit>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class DistributorVisit(
    val arrival_comment: String?,
    val arrival_image: String?,
    val arrival_latitude: String?,
    val arrival_longitude: String?,
    val arrival_time: String?,
    val created_at: String?,
    val departure_comment: String?,
    val departure_image: String?,
    val departure_latitude: String?,
    val departure_longitude: String?,
    val departure_time: String?,
    @PrimaryKey
    val distributor_advisor_atp_id: Int,
    val distributor_advisor_id: Int,
    val distributor_firm_name: String?,
    val distributor_name: String?,
    val distributor_type: String?,
    val duration: String = "0",
    val is_arrival_departure: Int?,
    val status: Int?,
    val updated_at: String?,
    val user_ecn: String?,
    val user_id: Int?,
    val user_name: String?,
    val user_type: String?
)