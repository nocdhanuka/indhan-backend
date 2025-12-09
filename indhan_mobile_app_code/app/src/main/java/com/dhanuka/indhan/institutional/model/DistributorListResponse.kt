package com.dhanuka.indhan.institutional.model

import androidx.room.Entity
import androidx.room.PrimaryKey

data class DistributorListResponse(
    val crr_date: String,
    val `data`: List<DistributorListData>,
    val message: String,
    val status: String,
    val status_code: Int
)

@Entity
data class DistributorListData(
    val address: String?,
    val brands: String?,
    val created_at: String?,
    val crope: String?,
    @PrimaryKey
    val distributor_advisor_id: Int,
    val distributor_firm_name: String?,
    val distributor_name: String?,
    val distributor_type: String?,
    val district: String?,
    val district_id: Int?,
    val is_distributor: Int = 0,
    var is_visited: Int = 0,
    val market: String?,
    val market_id: Int?,
    val mobile: String?,
    val mobile2: String?,
    val pincode: String?,
    val state: String?,
    val state_id: Int?,
    val status: Int?,
    val territory: String?,
    val territory_id: Int?,
    val turnover: String?,
    val updated_at: String?,
    val user_ecn: String?,
    val user_id: Int?,
    val user_name: String?,
    val user_type: String?
)