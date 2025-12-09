package com.dhanuka.indhan.institutional.model.sugarmill

import androidx.room.Entity
import androidx.room.PrimaryKey

data class SugarmillAtpListResponse(
    val `data`: List<SugarMillAtpListData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class SugarMillAtpListData(
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
    val sugarmill_atp_id: Int,
    val sugarmill_category_id: Int?,
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
    val sugarmill_id: Int?,
    val sugarmill_name: String?,
    val sugarmill_office_type: String?,
    val sugarmill_type: String?,
    val ticket_number: String?,
    val updated_at: String?,
    var typeOfData: String?,
    val distance: String?,
    val is_validate: String?
)