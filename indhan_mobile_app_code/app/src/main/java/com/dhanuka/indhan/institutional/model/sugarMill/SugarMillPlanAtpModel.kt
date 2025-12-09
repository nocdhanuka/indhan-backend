package com.dhanuka.indhan.institutional.model.sugarMill

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity
class SugarMillPlanAtpModel {
    @PrimaryKey(autoGenerate = true)
    var id: Int = 0
    var status: Int = 0
    var hitTime: Long = 0
    var dateTime: String = ""
    var flName: String = ""
    var flEcn: String = ""
    var flType: String = ""
    var apiToken: String = ""
    var companyCategory: String = ""
    var sugarMillId: String = ""
    var ticketNumber: String = ""
    var manager: String = ""
}