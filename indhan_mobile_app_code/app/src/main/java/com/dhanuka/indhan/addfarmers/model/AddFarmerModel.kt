package com.dhanuka.indhan.addfarmers.model

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity
class AddFarmerModel() {

    @PrimaryKey(autoGenerate = true)
    var _id: Int? = null
    var ECN: String? = null
    var apiToken: String? = null
    var villageId: String? = null
    var farmerName: String? = null
    var fatherName: String? = null
    var contactnumber: String? = null
    var alternateContactnumber: String? = null
    var cropId: String? = null
    var acres: String? = null
    var farmerLat: String? = null
    var farmerLong: String? = null
    var filePath: String? = null
    var fileName: String? = null
}