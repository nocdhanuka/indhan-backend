package com.dhanuka.indhan.institutional.model.influencers

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity
class InfluencersPlanAtpModel {
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
    var influencerId: String = ""
    var ticketNumber: String = ""
    var manager: String = ""
}