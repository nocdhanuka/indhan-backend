package com.dhanuka.indhan.institutional.model.sugarMill

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity
data class AddSugarMillBranchModel(
    @PrimaryKey
    var sugarmillId: Long,
    var apiToken: String?,
    var office_type: Int?,
    var sugarmill_type: Int?,
    var parent_sugarmill_id: String?,
    var district: String?,
    var state: String?,
    var name: String?,
    var turn_over: String?,
    var land_area: String?,
    var sugarmill_category_id: String?,
    var sugarmill_category_type: String?,
    var state_id: String?,
    var district_id: String?,
    var address: String?,
    var latitude: Double?,
    var longitude: Double?,
    var image: String?,
    var contact_data: String?,
    var fl_ecn: String?,
    var fl_name: String?,
    var fl_type: String?,
    var hitTime: Long?,
    var status: Int?
)
