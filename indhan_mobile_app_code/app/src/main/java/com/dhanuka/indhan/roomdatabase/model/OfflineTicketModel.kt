package com.dhanuka.indhan.roomdatabase.model

import androidx.room.Entity
import androidx.room.PrimaryKey

@Entity
class OfflineTicketModel {
    @PrimaryKey(autoGenerate = true)
    var _id: Int? = null

    var request: String? = null
}