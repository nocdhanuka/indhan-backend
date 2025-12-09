package com.dhanuka.indhan.DemoRequestActivity.models

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity
class DemoRequestOfflineModel {

    @PrimaryKey(autoGenerate = true)
    var _id: Int? = null
    var apiToken: String? = null
    var cropId: String? = null
    var productId: String? = null
    var farmerName: String? = null
    var stateId: String? = null
    var districtId: String? = null
    var tehsilId: String? = null
    var pincode: String? = null
    var villageId: String? = null
    var contactNumber: String? = null
    var showingDate: String? = null
    var spray: String? = null
    var latitude: String? = null
    var longitude: String? = null
    var farmer_image: String? = null
    var farmer_type: String? = null
    var fileName: String? = null
    var cropName: String? = null
    var productName: String? = null
}