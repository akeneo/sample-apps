from fastapi import APIRouter, Request

router = APIRouter()

@router.get("/first-api-call")
def read_root(request: Request, db: Session = Depends(get_db)):

    # Replace by the API endpoint you want to call. Here an example with channels
    # https://api.akeneo.com/api-reference.html#get_channels
    apiUrl = config['PIM_URL'] + '/api/rest/v1/channels'
    token = tokenRepository.get_token(db)
    response = requests.get(apiUrl,  headers={
        'Authorization': 'Bearer %s' % token.access_token,
        'X-APP-SOURCE':  'startApp-python',
    })

    return response.json()
