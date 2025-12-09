package com.dhanuka.indhan.acpfarmers


import com.google.gson.annotations.SerializedName

data class ACPFIlterBean(
    @SerializedName("data")
    val `data`: Data,
    @SerializedName("message")
    val message: String, // Department list!
    @SerializedName("status")
    val status: String, // success
    @SerializedName("status_code")
    val statusCode: Int // 200
) {
    data class Data(
        @SerializedName("ASM_list")
        val aSMList: List<ASM>,
        @SerializedName("BDO_list")
        val bDOList: List<BDO>,
        @SerializedName("DD_list")
        val dDList: List<DD>,
        @SerializedName("RSM_list")
        val rSMList: List<RSM>
    ) {
        data class DD(
            @SerializedName("id")
            val ddId: String, // 2
            @SerializedName("name")
            val name: String, // Deepak
            var isChecked: Boolean

        )

        data class BDO(
            @SerializedName("id")
            val id: String, // 5
            @SerializedName("name")
            val name: String // Dileep Kumar Padi
        )

        data class RSM(
            @SerializedName("id")
            val salesRegionalManagerId: String, // 26
            @SerializedName("name")
            val salesRegionalManagerName: String // G V Ramana
        )

        data class ASM(
            @SerializedName("id")
            val salesAreaManagerId: String, // 50
            @SerializedName("name")
            val salesAreaManagerName: String // S Srinivas
        )
    }
}