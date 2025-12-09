package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.MarkVisits.MarkVisitRequestModel

@Dao
interface MarkVisitDao {

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: MarkVisitRequestModel?)

    @Query("SELECT * FROM MarkVisitRequestModel")
    fun getAllMarkVisitData(): List<MarkVisitRequestModel?>?

    @Query("SELECT * FROM MarkVisitRequestModel WHERE isSyncedToServer =:isServer LIMIT 1")
    fun getSingleMarkVisitData(isServer: Boolean?): MarkVisitRequestModel?

    @Query("SELECT * FROM MarkVisitRequestModel LIMIT 1")
    fun getOnlyOneData(): MarkVisitRequestModel?

    @Query("UPDATE MarkVisitRequestModel SET isSyncedToServer = :data WHERE id = :id")
    fun updateMarkVisitUploaded(data: Boolean, id: Int?)

    @Query("DELETE FROM MarkVisitRequestModel WHERE id =:id")
    fun deleteTableData(id: Int)
}