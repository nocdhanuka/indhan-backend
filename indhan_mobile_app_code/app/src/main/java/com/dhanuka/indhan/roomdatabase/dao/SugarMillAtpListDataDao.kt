package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.institutional.model.sugarmill.SugarMillAtpListData

@Dao
interface SugarMillAtpListDataDao {
    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: SugarMillAtpListData)

    @Query("SELECT * FROM SugarMillAtpListData")
    fun getAllAtpListData(): List<SugarMillAtpListData?>?

    @Query("SELECT * FROM SugarMillAtpListData WHERE sugarmill_id =:id")
    fun getAllById(id: Int): List<SugarMillAtpListData?>?

    @Query("DELETE FROM SugarMillAtpListData WHERE sugarmill_atp_id =:id")
    fun deleteTableData(id: Int)

}