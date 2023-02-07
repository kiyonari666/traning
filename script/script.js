const comCreateForm = document.forms.comCreateForm

// comCreateForm.postal_code.addEventListener('input', event => {
//     if(event.target.value.length === 7) {
//         const postcode = event.target.value
//         fetch(`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${postcode}`)
//         .then(response => response.json())
//         .then(data => {
//             comCreateForm.prefecture_code.value = data.results[0].prefcode
//             comCreateForm.address.value = data.results[0].address2 + data.results[0].address3
//         })
//         .catch()
//     }
// })

comCreateForm.postal_code.addEventListener('input', event => {
    if(event.target.value.length === 7) {
        const postcode = event.target.value
        fetch(`https://zipcloud.ibsnet.co.jp/api/search?zipcode=${postcode}`)
        .then(response => response.json())
        .then(data => {
            comCreateForm.prefecture_code.value = data.results[0].prefcode
            comCreateForm.address.value = data.results[0].address2 + data.results[0].address3
        })
        .catch(error => {
            alert('郵便番号を認識できませんでした\nページ再読み込み後、もう一度入力して下さい', error)
        })
    }
})
