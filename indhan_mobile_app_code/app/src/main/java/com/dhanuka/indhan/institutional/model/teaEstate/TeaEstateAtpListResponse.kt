package com.dhanuka.indhan.institutional.model.teaEstate

import androidx.room.Entity
import androidx.room.PrimaryKey

data class TeaEstateAtpListResponse(
    val `data`: List<AtpListData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class AtpListData(
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
    val estate_atp_id: Int,
    val estate_category_id: Int?,
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
    val tea_estate_id: Int?,
    val tea_estate_name: String?,
    val tea_estate_office_type: String?,
    val tea_estate_type: String?,
    val ticket_number: String?,
    val updated_at: String?,
    var typeOfData: String?,
    val distance: String?,
    val is_validate: String?
)