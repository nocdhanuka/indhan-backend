package com.dhanuka.indhan.MarkVisits

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity
data class MarkVisitRequestModel(

    @PrimaryKey(autoGenerate = true)
    val id: Int,
    val villageId: String,
    val farmerId: String,
    val cropId: String,
    val villageName: String,
    val farmerName: String,
    val cropName: String,
    val imgFile: String,
    val imgName: String,
    val isSyncedToServer: Boolean = false
)