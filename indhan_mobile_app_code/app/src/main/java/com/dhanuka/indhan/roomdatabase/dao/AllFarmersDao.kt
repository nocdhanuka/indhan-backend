package com.dhanuka.indhan.roomdatabase.dao

import androidx.room.Dao
import androidx.room.Insert
import androidx.room.OnConflictStrategy
import androidx.room.Query
import com.dhanuka.indhan.farmerlist.FarmerBean.VillageFarmerData

@Dao
interface AllFarmersDao {
    @Insert
    fun insert(data: List<VillageFarmerData?>?)

    @Insert(onConflict = OnConflictStrategy.REPLACE)
    fun insert(data: VillageFarmerData)

    @Query("SELECT * FROM VillageFarmerData WHERE village_id =:villageId")
    fun getVillageAllFarmerList(villageId: String?): List<VillageFarmerData?>?

    @get:Query("SELECT * FROM VillageFarmerData")
    val allFarmerList: List<VillageFarmerData?>?

    @Query("DELETE FROM VillageFarmerData")
    fun deleteTableData()
}