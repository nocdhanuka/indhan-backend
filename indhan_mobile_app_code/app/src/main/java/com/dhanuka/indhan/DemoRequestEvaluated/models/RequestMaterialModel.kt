package com.dhanuka.indhan.DemoRequestEvaluated.models

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity
class RequestMaterialModel {
    @PrimaryKey(autoGenerate = true)
    var id: Int = 0
    var apiToken: String? = null
    var product_id: String? = null
    var product_name: String? = null
    var crop_id: String? = null
    var crop_name: String? = null
    var expected_demos: String? = null
    var need_by_date: String? = null
    var requested_units: String? = null
    var godown_id: String? = null
    var godown_name: String? = null
    var isUploaded: Boolean? = false
    var timeStamp: Long? = null
}