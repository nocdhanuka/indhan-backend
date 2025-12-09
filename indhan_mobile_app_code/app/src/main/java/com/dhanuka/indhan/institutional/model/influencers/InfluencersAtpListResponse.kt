package com.dhanuka.indhan.institutional.model.influencers

import androidx.room.Entity
import androidx.room.PrimaryKey

data class InfluencersAtpListResponse(
    val `data`: List<InfluencersAtpListData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class InfluencersAtpListData(
    val arrival_comment: String?,
    val arrival_image: String?,
    val arrival_latitude: String?,
    val arrival_longitude: String?,
    val arrival_time: String?,
    val company_category: String?,
    val created_at: String?,
    val date_time: String?,
    val duration: String?,
    val departure_comment: String?,
    val departure_image: String?,
    val departure_latitude: String?,
    val departure_longitude: String?,
    val departure_time: String?,
    val district: String?,
    val district_id: Int?,
    @PrimaryKey
    val influencer_atp_id: Int,
    val influencer_category_id: Int?,
    val fl_ecn: String?,
    val fl_name: String?,
    val fl_type: String?,
    var is_arrival_departure: Int?,
    val manager_ecn: String?,
    val manager_name: String?,
    val manager_type: String?,
    val state: String?,
    val state_id: Int?,
    val status: Int?,
    val influencer_id: Int?,
    val influencer_name: String?,
    val influencer_office_type: String?,
    val influencer_type: String?,
    val ticket_number: String?,
    val updated_at: String?,
    var typeOfData: String?,
    val distance: String?,
    val is_validate: String?
)