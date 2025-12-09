package com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity(tableName = "DurationTable")
data class ActivityDurationModel(
    var id: Int = 0,
    @PrimaryKey
    var ticket_no: String = "",
    var activity_duration: Double = 0.0,
    var total_pda: String = "0",
    var total_activity_distance: Double = 0.0,
    var status: Int = 0
)