package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.Activity.offlineClaimTicket.ManualTicketClaimImage

@Dao
interface ManualTicketClaimDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: ManualTicketClaimImage)

    @Query("SELECT * FROM ManualTicketClaimImage WHERE type >=:type")
    fun getAllManualSelfTicketClaimData(
        type: Int,
    ): List<ManualTicketClaimImage>?

    @Query("SELECT * FROM ManualTicketClaimImage WHERE type =:type")
    fun getAllManualTicketClaimData(
        type: Int,
    ): List<ManualTicketClaimImage>?

    @Query("SELECT * FROM ManualTicketClaimImage")
    fun getAllManualTicketClaimData(): List<ManualTicketClaimImage>?

    @Query("SELECT * FROM ManualTicketClaimImage WHERE isUploaded =:status LIMIT 1")
    fun getSingleManualTicketClaimData(status: Int): ManualTicketClaimImage?

    @Query("SELECT * FROM ManualTicketClaimImage WHERE isUploaded =:status AND ticket_number =:ticket_no LIMIT 1")
    fun getSingleManualTicketClaimData(status: Int, ticket_no: String): ManualTicketClaimImage?

    @Query("SELECT * FROM ManualTicketClaimImage WHERE isUploaded =:status AND lastHitTime<=:time AND type =:type LIMIT 1")
    fun getSingleManualTicketClaimData(status: Int, time: Long, type: Int): ManualTicketClaimImage?

    @Query("SELECT * FROM ManualTicketClaimImage WHERE lastHitTime<=:time LIMIT 1")
    fun getSosManualTicketClaimData(time: Long): ManualTicketClaimImage?

    @Query("UPDATE ManualTicketClaimImage SET isUploaded =:status WHERE id=:id AND ticket_number =:ticketNumber AND type =:type")
    fun updateManualTicketClaimData(id: Int, status: Int, ticketNumber: String, type: Int)

    @Query("UPDATE ManualTicketClaimImage SET isUploaded =:status WHERE id=:id")
    fun updateManualTicketClaimData(id: Int, status: Int)

    @Query("UPDATE ManualTicketClaimImage SET isUploaded=:status ,lastHitTime=:time WHERE id=:id AND ticket_number =:ticketNumber")
    fun updateManualTicketClaimData(
        id: Int,
        status: Int,
        ticketNumber: String,
        time: Long
    )

    @Query("UPDATE ManualTicketClaimImage SET type =:type ,departure_time=:departure_time ,departure_comment =:departure_comment ,departure_latitude=:departure_lat ,departure_longitude=:departure_lng ,departure_imageURL=:departure_img ,duration=:duration WHERE id=:id")
    fun updateManualArrivalDepartureData(
        id: Int,
        type: Int,
        departure_time: String,
        departure_comment: String,
        departure_lat: String,
        departure_lng: String,
        departure_img: String,
        duration: String
    )

    @Query("DELETE FROM ManualTicketClaimImage WHERE id =:id")
    fun deleteManualTicketClaimData(id: Int)

}